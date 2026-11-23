import type { Board } from '../../../shared/types'
import { Modal } from '../../../shared/ui'
import { CreateBoardForm } from './CreateBoardForm'

type CreateBoardModalProps = {
  isOpen: boolean
  onClose: () => void
  onCreated: (board: Board) => void
}

export function CreateBoardModal({ isOpen, onClose, onCreated }: CreateBoardModalProps) {
  return (
    <Modal isOpen={isOpen} title="Новая доска" onClose={onClose}>
      {/* Форма монтируется только при открытии — так её состояние сбрасывается */}
      {isOpen && <CreateBoardForm onCreated={onCreated} onCancel={onClose} />}
    </Modal>
  )
}
