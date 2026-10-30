<?php declare(strict_types=1);

namespace App\Library\Auth;

use App\Library\Database\Database;
use App\Library\Super;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use SessionHandlerInterface;

/**
 * Обрабатывает сессии с помощью БД:
 * - Предотвращает состояние гонки, за счёт блокировки в БД.
 * - Не хранит сами идентификаторы сессий, что бы кража БД не позволяла угнать сессию.
 * - Очистка по крону.
 *
 * Правила работы:
 * - session_start()
 * - после авторизации и до отправки контента session_regenerate_id(true)
 * - session_write_close() как только разобрались с авторизацией, что бы отпустить блокировку
 */
final class SessionHandler implements SessionHandlerInterface {
    public const string SESSIONS_TABLE_NAME = 'sessions';

    /**
     * Неделя + 200 секунд на завершение параллельных запросов с той же сессией,
     * должно соотноситься с session.cookie_lifetime их php.ini
     */
    public const int SESSION_EXPIRATION_SECONDS = 605000;

    private object $db;

    private bool $isTransactionActive = false;

    /**
     * Вызывается сразу после session_start()
     */
    public function open(string $path, string $name): bool {
        $this->db = Database::getInstance();
        return true;
    }

    /**
     * Вызывается после open()
     */
    public function read(string $id): string|false {
        $hashedId = $this->hash($id);

        try {
            if (!$this->isTransactionActive) {
                $this->db::beginTransaction();
                $this->isTransactionActive = true;
            }

            // Блокируем сессию через SELECT ... FOR UPDATE
            $session = $this->db
                ->table(self::SESSIONS_TABLE_NAME)
                ->where('hashed_id', $hashedId)
                ->lockForUpdate()
                ->first();

            if ($session) {
                return $session->data;
            }

            // Если сессии нет — делаем атомарный UPSERT ("пустышку") с заготовкой новой сессии
            // В PostgreSQL это сгенерирует правильный INSERT ... ON CONFLICT DO NOTHING
            $this->db
                ->table(self::SESSIONS_TABLE_NAME)
                ->insertOrIgnore([
                    'hashed_id'           => $hashedId,
                    'data'                => '',
                    'expiration_datetime' => Carbon::now()->addSeconds(self::SESSION_EXPIRATION_SECONDS),
                ]);

            // Если сессия только создалась, тоже блокируем её
            $session = $this->db
                ->table(self::SESSIONS_TABLE_NAME)
                ->where('hashed_id', $hashedId)
                ->lockForUpdate()
                ->first();

            return $session ? $session->data : '';

        } catch (QueryException $e) {
            Super::log()->error($e->getMessage());

            if ($this->isTransactionActive) {
                $this->db::rollBack();
                $this->isTransactionActive = false;
            }

            return '';
        }
    }

    public function write(string $id, string $data): bool {
        $hashedId = $this->hash($id);

        try {
            // Тут ещё раз пробуем открыть транзакцию, если был вызван
            // session_regenerate_id(true) удаливший старую сессию и завершивший транзакцию.
            if (!$this->isTransactionActive) {
                $this->db::beginTransaction();
                $this->isTransactionActive = true;
            }

            // Обновим или создадим сессию
            // В PostgreSQL INSERT ... ON CONFLICT (id) DO UPDATE
            $this->db
                ->table(self::SESSIONS_TABLE_NAME)
                ->upsert(
                    [
                        [
                            'hashed_id'           => $hashedId,
                            'data'                => $data,
                            'expiration_datetime' => Carbon::now()->addSeconds(self::SESSION_EXPIRATION_SECONDS),
                        ],
                    ],
                    ['hashed_id'],      // Уникальный ключ для проверки конфликта
                    ['data'],           // Что обновлять в случае конфликта (дату специально не обновляем!)
                );

            return true;
        } catch (QueryException $e) {
            Super::log()->error($e->getMessage());

            if ($this->isTransactionActive) {
                $this->db::rollBack();
                $this->isTransactionActive = false;
            }

            return false;
        }
    }

    /**
     * Вызывается после write() и автоматом при завершении скрипта
     */
    public function close(): bool {
        if ($this->isTransactionActive) {
            $this->db::commit();
            $this->isTransactionActive = false;
        }

        return true;
    }

    public function destroy(string $id): bool {
        $hashedId = $this->hash($id);

        try {
            $this->db
                ->table(self::SESSIONS_TABLE_NAME)
                ->where('hashed_id', $hashedId)
                ->delete();

            if ($this->isTransactionActive) {
                $this->db::commit();
                $this->isTransactionActive = false;
            }

            return true;
        } catch (QueryException $e) {
            Super::log()->error($e->getMessage());

            if ($this->isTransactionActive) {
                $this->db::rollBack();
                $this->isTransactionActive = false;
            }

            return false;
        }
    }

    /**
     * Не используем вероятностный механизм, очистка по крону.
     * @see RemoveOldSessionsCronTask
     */
    public function gc(int $max_lifetime): int|false {
        return true;
    }

    /**
     * Не храним id сессии в БД, только хеш (64 символа)
     */
    private function hash(string $id): string {
        return hash('sha256', $id);
    }
}
