import { useBoardForm } from '../../../shared/lib/form'
import { fieldError, validateBoardTitle } from '../../../shared/lib/validation'
import type { Board } from '../../../shared/types'
import { editBoard } from '../api'

type EditBoardFormValues = {
  title: string
}

type EditBoardOptions = {
  board: Board
  onUpdated: (board: Board) => void
}

export function useEditBoardForm({ board, onUpdated }: EditBoardOptions) {
  const { setValue, ...form } = useBoardForm<EditBoardFormValues>({
    initialValues: { title: board.title },
    validate: (values) => fieldError('title', validateBoardTitle(values.title)),
    onSubmit: async (values) => {
      onUpdated(await editBoard({ board_id: board.board_id, title: values.title.trim() }))
    },
  })

  return { ...form, setTitle: (value: string) => setValue('title', value) }
}
