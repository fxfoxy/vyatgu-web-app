import type { BoardColumn } from '../../../shared/types'
import { Alert, Button, ColorSwitchPicker, Input } from '../../../shared/ui'
import { useCreateColumnForm } from '../model/useCreateColumnForm'

type CreateColumnFormProps = {
  boardId: number
  ordinalPosition: number
  onCreated: (column: BoardColumn) => void
  onCancel: () => void
}

export function CreateColumnForm({
  boardId,
  ordinalPosition,
  onCreated,
  onCancel,
}: CreateColumnFormProps) {
  const { values, errors, formError, isSubmitting, setTitle, setColor, submit } =
    useCreateColumnForm({ boardId, ordinalPosition, onCreated })

  return (
    <form className="flex flex-col gap-4" onSubmit={submit} noValidate>
      <Alert message={formError} />

      <Input
        name="title"
        label="Название"
        value={values.title}
        onChange={(_, value) => setTitle(value)}
        error={errors.title}
        placeholder="Например, В работе"
        maxLength={255}
        autoFocus
      />

      <ColorSwitchPicker value={values.color} onChange={setColor} error={errors.color} />

      <div className="flex justify-end gap-2">
        <Button type="button" variant="secondary" onClick={onCancel} disabled={isSubmitting}>
          Отмена
        </Button>
        <Button type="submit" isLoading={isSubmitting}>
          Создать
        </Button>
      </div>
    </form>
  )
}
