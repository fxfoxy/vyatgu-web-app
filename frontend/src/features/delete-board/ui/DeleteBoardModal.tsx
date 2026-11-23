import type { Board } from '../../../shared/types'
import { Alert, Button, Modal } from '../../../shared/ui'
import { useDeleteBoard } from '../model/useDeleteBoard'

type DeleteBoardModalProps = {
  isOpen: boolean
  board: Board
  onClose: () => void
  onDeleted: () => void
}

export function DeleteBoardModal({ isOpen, board, onClose, onDeleted }: DeleteBoardModalProps) {
  return (
    <Modal isOpen={isOpen} title="Удалить доску?" onClose={onClose}>
      {isOpen && <DeleteBoardContent board={board} onCancel={onClose} onDeleted={onDeleted} />}
    </Modal>
  )
}

type DeleteBoardContentProps = {
  board: Board
  onCancel: () => void
  onDeleted: () => void
}

function DeleteBoardContent({ board, onCancel, onDeleted }: DeleteBoardContentProps) {
  const { isSubmitting, error, submit } = useDeleteBoard(board.board_id, onDeleted)

  return (
    <div className="flex flex-col gap-4">
      <Alert message={error} />

      <p className="text-sm text-slate-600">
        Доска «{board.title}» будет удалена. Это действие нельзя отменить.
      </p>

      <div className="flex justify-end gap-2">
        <Button variant="secondary" onClick={onCancel} disabled={isSubmitting}>
          Отмена
        </Button>
        <Button
          variant="danger"
          isLoading={isSubmitting}
          onClick={() => {
            void submit()
          }}
        >
          Удалить
        </Button>
      </div>
    </div>
  )
}
