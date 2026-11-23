import { Link, useParams } from 'react-router'
import { useBoards } from '../../features/boards-list'
import { Alert, Button } from '../../shared/ui'
import { BoardView } from './ui/BoardView'

export function BoardPage() {
  const { boardId } = useParams()
  const numericBoardId = Number(boardId)
  const { boards, isLoading, error, reload, updateBoard } = useBoards()
  const board = boards.find((item) => item.board_id === numericBoardId)

  if (isLoading) {
    return <p className="py-16 text-center text-slate-500">Загрузка…</p>
  }

  if (error) {
    return (
      <div className="flex flex-col items-start gap-3">
        <Alert message={error} />
        <Button variant="secondary" onClick={reload}>
          Повторить
        </Button>
      </div>
    )
  }

  if (!board) {
    return (
      <div className="flex flex-col items-center gap-4 py-16 text-center">
        <h1 className="text-2xl font-semibold text-slate-900">Доска не найдена</h1>
        <p className="text-slate-600">
          Возможно, она была удалена или принадлежит другому пользователю.
        </p>
        <Link to="/" className="font-medium text-indigo-600 hover:text-indigo-700">
          На главную
        </Link>
      </div>
    )
  }

  return <BoardView key={board.board_id} board={board} onBoardUpdated={updateBoard} />
}
