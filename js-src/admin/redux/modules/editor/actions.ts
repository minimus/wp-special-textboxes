import type { TDispatch, TStyle } from '../../../../types/admin'
import { ERROR, START, SUCCESS, EDITOR_POST_DATA, EDITOR_GET_DATA } from '../../constants'
import { getData, postData } from '../../helpers'
import type { IReducers } from '../../../../types/state'
import { OptionsObject, SnackbarKey, SnackbarMessage } from "notistack";

export const defaultStyle: TStyle = {
	slug: 'custom',
	type: 'custom',
	caption: 'Custom Style',
	colors: {
		body: { color: '#000000', background: ['#f7cdf5', '#f7cdf5'] },
		border: { color: '#f844ee' },
		caption: { color: '#ffffff', background: ['#f844ee', '#f844ee'] },
		image: { image: 'heart.png', defaultImage: '', enabled: false },
	},
	trash: 0,
}

export const getEditorData =
	(slug: string) =>
	async (dispatch: TDispatch): Promise<Record<string, unknown>> => {
		const currSlug = slug ?? 'custom'

		dispatch({ type: EDITOR_GET_DATA + START })

		try {
			const { result, data } = await getData(`colors/${currSlug}`)
			if (result === 'ok') {
				dispatch({
					type: EDITOR_GET_DATA + SUCCESS,
					payload: slug
						? data
						: {
								...data,
								slug: `custom-${new Date().valueOf()}`,
								type: 'custom',
								caption: 'New Custom Style',
						  },
				})
				return data
			}
			dispatch({ type: EDITOR_GET_DATA + ERROR, error: 'WTF' })
			return null
		} catch (error: unknown) {
			dispatch({ type: EDITOR_GET_DATA + ERROR, error })
			return null
		}
	}

export const saveEditorData =
	(slug: string) =>
	async (
		dispatch: TDispatch,
		getState: () => IReducers,
		enqueueSnackbar: (message: SnackbarMessage, options?: OptionsObject) => SnackbarKey,
	): Promise<boolean | number | null | Record<string, unknown>> => {
		const { editor: { style = {} } = {}, locales: { data } = {} } = getState()
		const { messages: { editor: { savingSuccess = '', savingError = '' } = {} } = {} } = data ?? {}
		const body = JSON.stringify(style)
		dispatch({ type: EDITOR_POST_DATA + START })

		try {
			const { result, completed } = await postData(`colors/${slug}`, body)
			if (result === 'ok') {
				dispatch({ type: EDITOR_POST_DATA + SUCCESS, payload: completed })
				enqueueSnackbar(savingSuccess, { variant: 'success' })
				return completed
			}
			dispatch({ type: EDITOR_POST_DATA + ERROR, error: 'Unknown error' })
			enqueueSnackbar(savingError, { variant: 'error' })
			return null
		} catch (error: unknown) {
			dispatch({ type: EDITOR_POST_DATA + ERROR, error })
			enqueueSnackbar(savingError, { variant: 'error' })
			return null
		}
	}
