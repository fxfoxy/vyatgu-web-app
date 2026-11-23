import { request } from '../../shared/lib/api'
import type { Board } from '../../shared/types'

export type EditBoardInput = {
  board_id: number
  title: string
}

export async function editBoard(input: EditBoardInput): Promise<Board> {
  return request<Board>('/api/frontend/edit-board', {
    method: 'POST',
    // Публичные доски пока не поддерживаются, но бэкенд требует is_public явно:
    // без этого поля дефолт на бэкенде падает с invalid_boolean.
    body: { ...input, is_public: false },
  })
}
