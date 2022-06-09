import type { TDispatch } from '../../../../types/admin'
import { ERROR, START, SUCCESS, LOCALIZATION_GET_STRINGS } from '../../constants'
import { getData } from '../../helpers'

export const getLocalizationStrings =
	() =>
	async (dispatch: TDispatch): Promise<Record<string, unknown>> => {
		dispatch({ type: LOCALIZATION_GET_STRINGS + START })

		try {
			const { result, data } = await getData('locale')
			if (result === 'ok') {
				dispatch({ type: LOCALIZATION_GET_STRINGS + SUCCESS, payload: data })
				return data
			}
			dispatch({ type: LOCALIZATION_GET_STRINGS + ERROR, error: 'WTF' })
			return null
		} catch (error: unknown) {
			dispatch({ type: LOCALIZATION_GET_STRINGS + ERROR, error })
			return null
		}
	}
