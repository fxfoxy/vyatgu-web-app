import type { Board } from '../../../shared/types'
import { Alert, Button } from '../../../shared/ui'
import { BoardCard } from './BoardCard'

type BoardsListProps = {
  boards: Board[]
  isLoading: boolean
  error: string
  onRetry: () => void
  onCreate: () => void
}

export function BoardsList({ boards, isLoading, error, onRetry, onCreate }: BoardsListProps) {
  if (isLoading) {
    return (
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" aria-busy="true">
        {Array.from({ length: 3 }, (_, index) => (
          <div
            key={index}
            className="h-28 animate-pulse rounded-lg border border-slate-200 bg-white"
          />
        ))}
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex flex-col items-start gap-3">
        <Alert message={error} />
        <Button variant="secondary" onClick={onRetry}>
          Повторить
        </Button>
      </div>
    )
  }

  if (boards.length === 0) {
    return (
      <div className="flex flex-col items-center gap-3 rounded-lg border border-dashed border-slate-300 bg-white p-10 text-center">
        <h2 className="text-lg font-semibold text-slate-900">Пока нет досок</h2>
        <p className="max-w-md text-slate-600">Создайте первую доску, чтобы начать работу.</p>
        <Button onClick={onCreate}>Создать доску</Button>
      </div>
    )
  }

  return (
    <ul className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      {boards.map((board) => (
        <li key={board.board_id}>
          <BoardCard board={board} />
        </li>
      ))}
    </ul>
  )
}
