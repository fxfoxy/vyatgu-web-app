import { request } from '../../shared/lib/api'

export async function deleteBoard(boardId: number): Promise<void> {
  await request('/api/frontend/delete-board', {
    method: 'POST',
    body: { board_id: boardId },
  })
}
