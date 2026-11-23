import { request } from '../../shared/lib/api'
import type { Board } from '../../shared/types'

export type CreateBoardInput = {
  title: string
}

export async function createBoard(input: CreateBoardInput): Promise<Board> {
  return request<Board>('/api/frontend/create-board', {
    method: 'POST',
    // Публичные доски пока не поддерживаются, но бэкенд требует is_public явно:
    // без этого поля дефолт на бэкенде падает с invalid_boolean.
    body: { title: input.title, is_public: false },
  })
}
