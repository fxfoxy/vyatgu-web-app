import type { Board } from '../../../shared/types'
import { Alert, Button, Input } from '../../../shared/ui'
import { useEditBoardForm } from '../model/useEditBoardForm'

type EditBoardFormProps = {
  board: Board
  onUpdated: (board: Board) => void
  onCancel: () => void
}

export function EditBoardForm({ board, onUpdated, onCancel }: EditBoardFormProps) {
  const { values, errors, formError, isSubmitting, setTitle, submit } = useEditBoardForm({
    board,
    onUpdated,
  })

  return (
    <form className="flex flex-col gap-4" onSubmit={submit} noValidate>
      <Alert message={formError} />

      <Input
        name="title"
        label="Название"
        value={values.title}
        onChange={(_, value) => setTitle(value)}
        error={errors.title}
        placeholder="Например, Учёба"
        maxLength={255}
        autoFocus
      />

      <div className="flex justify-end gap-2">
        <Button type="button" variant="secondary" onClick={onCancel} disabled={isSubmitting}>
          Отмена
        </Button>
        <Button type="submit" isLoading={isSubmitting}>
          Сохранить
        </Button>
      </div>
    </form>
  )
}
