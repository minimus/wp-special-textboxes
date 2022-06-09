import type { TDispatch, TStyle } from '../../../../types/admin'
import { getData, postData } from '../../helpers'
import { ERROR, START, SUCCESS, STYLES_FETCH_DATA, STYLES_SET_TRASH } from '../../constants'

export const getStylesData =
	(filter = 1) =>
	async (dispatch: TDispatch): Promise<Record<string, unknown>> => {
		dispatch({ type: STYLES_FETCH_DATA + START })

		try {
			const { data } = await getData(`styles/${filter}`)
			dispatch({ type: STYLES_FETCH_DATA + SUCCESS, payload: data })
			return data
		} catch (error: unknown) {
			dispatch({ type: STYLES_FETCH_DATA + ERROR, error })
			return null
		}
	}

export const setStyleTrash =
	(slug: string, style: TStyle, trash = 1) =>
	async (dispatch: TDispatch): Promise<number | boolean | Record<string, unknown>> => {
		const body: string = JSON.stringify({ ...style, trash })
		dispatch({ type: STYLES_SET_TRASH + START })

		try {
			const { result, completed } = await postData(`colors/${slug}`, body)
			if (result === 'ok') {
				dispatch({ type: STYLES_SET_TRASH + SUCCESS, payload: completed })
				return completed
			}
			dispatch({ type: STYLES_SET_TRASH + ERROR, error: 'WTF' })
			return null
		} catch (error: unknown) {
			dispatch({ type: STYLES_SET_TRASH + ERROR, error })
			return null
		}
	}
