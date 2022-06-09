import type { TDispatch } from '../../../../types/admin'
import { ERROR, START, SUCCESS, THEMES_ACTIVATE_THEME, THEMES_LOADING_THEMES_INFO } from '../../constants'
import { getData, postData } from '../../helpers'
import { getPluginSettings } from '../settings/actions'

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
	async (dispatch: TDispatch): Promise<boolean | null> => {
		dispatch({ type: THEMES_ACTIVATE_THEME + START, payload: slug })

		try {
			const { result } = await postData(`themes/${slug}`)
			if (result === 'ok') {
				await Promise.all([getThemesInfo()(dispatch), getPluginSettings()(dispatch)])
				// await getThemesInfo()(dispatch)
				// await getPluginSettings()(dispatch)
				return true
			}
			dispatch({ type: THEMES_ACTIVATE_THEME + ERROR, error: 'WTF' })
			return null
		} catch (error) {
			dispatch({ type: THEMES_ACTIVATE_THEME + ERROR, error })
			return null
		}
	}
