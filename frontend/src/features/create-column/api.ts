import { request } from '../../shared/lib/api'
import type { BoardColumn } from '../../shared/types'

export type CreateColumnInput = {
  board_id: number
  ordinal_position: number
  title: string
  /** 'rrggbb' без '#' или null — «без цвета». */
  color: string | null
}

export async function createColumn(input: CreateColumnInput): Promise<BoardColumn> {
  return request<BoardColumn>('/api/frontend/create-board-column', {
    method: 'POST',
    body: input,
  })
}
