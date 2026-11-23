import type { BoardColumn } from '../../../shared/types'
import { Modal } from '../../../shared/ui'
import { CreateColumnForm } from './CreateColumnForm'

type CreateColumnModalProps = {
  isOpen: boolean
  boardId: number
  ordinalPosition: number
  onClose: () => void
  onCreated: (column: BoardColumn) => void
}

export function CreateColumnModal({
  isOpen,
  boardId,
  ordinalPosition,
  onClose,
  onCreated,
}: CreateColumnModalProps) {
  return (
    <Modal isOpen={isOpen} title="Новая колонка" onClose={onClose}>
      {/* Форма монтируется только при открытии — так её состояние сбрасывается */}
      {isOpen && (
        <CreateColumnForm
          boardId={boardId}
          ordinalPosition={ordinalPosition}
          onCreated={onCreated}
          onCancel={onClose}
        />
      )}
    </Modal>
  )
}
