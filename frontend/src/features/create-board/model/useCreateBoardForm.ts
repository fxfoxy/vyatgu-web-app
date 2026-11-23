import { useBoardForm } from '../../../shared/lib/form'
import { fieldError, validateBoardTitle } from '../../../shared/lib/validation'
import type { Board } from '../../../shared/types'
import { createBoard } from '../api'

type CreateBoardFormValues = {
  title: string
}

export function useCreateBoardForm(onCreated: (board: Board) => void) {
  const { setValue, ...form } = useBoardForm<CreateBoardFormValues>({
    initialValues: { title: '' },
    validate: (values) => fieldError('title', validateBoardTitle(values.title)),
    onSubmit: async (values) => {
      onCreated(await createBoard({ title: values.title.trim() }))
    },
  })

  return { ...form, setTitle: (value: string) => setValue('title', value) }
}
