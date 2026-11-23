import { request } from '../../shared/lib/api'
import type { Board } from '../../shared/types'

/**
 * Список досок текущего пользователя.
 */
export async function getBoards(): Promise<Board[]> {
  const data = await request<{ boards: Board[] }>('/api/frontend/boards')
  return data.boards
}
