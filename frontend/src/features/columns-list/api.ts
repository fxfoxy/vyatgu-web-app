import { request } from '../../shared/lib/api'
import type { BoardColumn } from '../../shared/types'

/**
 * Колонки доски. Бэкенд отдаёт их через POST с board_id в теле
 * и сортирует по ordinal_position ASC.
 */
export async function getBoardColumns(boardId: number): Promise<BoardColumn[]> {
  const data = await request<{ board_columns: BoardColumn[] }>('/api/frontend/board-columns', {
    method: 'POST',
    body: { board_id: boardId },
  })
  return data.board_columns
}
