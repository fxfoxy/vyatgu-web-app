import type { Board } from '../../../shared/types'
import { Modal } from '../../../shared/ui'
import { EditBoardForm } from './EditBoardForm'

type EditBoardModalProps = {
  isOpen: boolean
  board: Board
  onClose: () => void
  onUpdated: (board: Board) => void
}

export function EditBoardModal({ isOpen, board, onClose, onUpdated }: EditBoardModalProps) {
  return (
    <Modal isOpen={isOpen} title="Редактировать доску" onClose={onClose}>
      {/* Форма монтируется только при открытии, поэтому берёт актуальное название */}
      {isOpen && <EditBoardForm board={board} onUpdated={onUpdated} onCancel={onClose} />}
    </Modal>
  )
}
