import { useState } from 'react'
import { Link, useNavigate } from 'react-router'
import { ColumnsList, useBoardColumns } from '../../../features/columns-list'
import { CreateColumnModal } from '../../../features/create-column'
import { DeleteBoardModal } from '../../../features/delete-board'
import { EditBoardModal } from '../../../features/edit-board'
import type { Board, BoardColumn } from '../../../shared/types'
import { Button } from '../../../shared/ui'

type BoardViewProps = {
  board: Board
  onBoardUpdated: (board: Board) => void
}

export function BoardView({ board, onBoardUpdated }: BoardViewProps) {
  const navigate = useNavigate()
  const { columns, isLoading, error, reload, addColumn, nextOrdinal, canAddColumn } =
    useBoardColumns(board.board_id)
  const [isCreatingColumn, setIsCreatingColumn] = useState(false)
  const [isEditingBoard, setIsEditingBoard] = useState(false)
  const [isDeletingBoard, setIsDeletingBoard] = useState(false)

  const handleColumnCreated = (column: BoardColumn) => {
    addColumn(column)
    setIsCreatingColumn(false)
  }

  return (
    <div className="flex flex-col gap-6">
      <div className="flex flex-wrap items-start justify-between gap-4">
        <div className="flex min-w-0 flex-col gap-1">
          <Link to="/" className="text-sm font-medium text-indigo-600 hover:text-indigo-700">
            ← Мои доски
          </Link>
          <h1 className="text-2xl font-semibold break-words text-slate-900">{board.title}</h1>
        </div>

        <div className="flex flex-wrap gap-2">
          <Button onClick={() => setIsCreatingColumn(true)} disabled={!canAddColumn}>
            Добавить колонку
          </Button>
          <Button variant="secondary" onClick={() => setIsEditingBoard(true)}>
            Изменить
          </Button>
          <Button variant="danger" onClick={() => setIsDeletingBoard(true)}>
            Удалить доску
          </Button>
        </div>
      </div>

      <ColumnsList
        columns={columns}
        isLoading={isLoading}
        error={error}
        canAddColumn={canAddColumn}
        onRetry={reload}
        onCreate={() => setIsCreatingColumn(true)}
      />

      <CreateColumnModal
        isOpen={isCreatingColumn}
        boardId={board.board_id}
        ordinalPosition={nextOrdinal ?? 0}
        onClose={() => setIsCreatingColumn(false)}
        onCreated={handleColumnCreated}
      />

      <EditBoardModal
        isOpen={isEditingBoard}
        board={board}
        onClose={() => setIsEditingBoard(false)}
        onUpdated={(updatedBoard) => {
          onBoardUpdated(updatedBoard)
          setIsEditingBoard(false)
        }}
      />

      <DeleteBoardModal
        isOpen={isDeletingBoard}
        board={board}
        onClose={() => setIsDeletingBoard(false)}
        onDeleted={() => navigate('/', { replace: true })}
      />
    </div>
  )
}
