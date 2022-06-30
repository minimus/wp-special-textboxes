import type { ILocaleMessages, TDispatch } from '../../../../types/admin'
import { ERROR, START, SUCCESS, THEMES_ACTIVATE_THEME, THEMES_LOADING_THEMES_INFO } from '../../constants'
import { getData, postData } from '../../helpers'
import { getPluginSettings } from '../settings/actions'
import { OptionsObject, SnackbarKey, SnackbarMessage } from 'notistack'

export const getThemesInfo =
	() =>
	async (dispatch: TDispatch): Promise<Record<string, unknown>> => {
		dispatch({ type: THEMES_LOADING_THEMES_INFO + START })

		try {
			const { data } = await getData('themes')
			dispatch({ type: THEMES_LOADING_THEMES_INFO + SUCCESS, payload: data })
			return data
		} catch (error: unknown) {
			dispatch({ type: THEMES_LOADING_THEMES_INFO + ERROR, error })
			return null
		}
	}

export const activateTheme =
	(slug: string) =>
	async (
		dispatch: TDispatch,
		enqueueSnackbar: (message: SnackbarMessage, options?: OptionsObject) => SnackbarKey,
		messages: ILocaleMessages,
	): Promise<boolean | null> => {
		const { savingSuccess, savingError } = messages
		dispatch({ type: THEMES_ACTIVATE_THEME + START, payload: slug })

		try {
			const { result } = await postData(`themes/${slug}`)
			if (result === 'ok') {
				await Promise.all([getThemesInfo()(dispatch), getPluginSettings()(dispatch)])
				enqueueSnackbar(savingSuccess, { variant: 'success' })
				return true
			}
			dispatch({ type: THEMES_ACTIVATE_THEME + ERROR, error: 'WTF' })
			enqueueSnackbar(savingError, { variant: 'error' })
			return null
		} catch (error: unknown) {
			dispatch({ type: THEMES_ACTIVATE_THEME + ERROR, error })
			enqueueSnackbar(savingError, { variant: 'error' })
			return null
		}
	}
