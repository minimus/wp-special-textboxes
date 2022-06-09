import { IReducers, IWindowWithOptions } from '../../../../types/state'
import { TDispatch, TOptions } from '../../../../types/admin'
import { getData, postData } from '../../helpers'
import {
	ERROR,
	START,
	SUCCESS,
	SETTINGS_SAVE_SETTINGS,
	SETTINGS_GET_SETTINGS,
	SETTINGS_GET_OPTIONS,
} from '../../constants'
import { OptionsObject, SnackbarKey, SnackbarMessage } from 'notistack'

export const getPluginOptions = () => (dispatch: TDispatch) => {
	const options: TOptions = (window as IWindowWithOptions).stbUserOptions

	dispatch({ type: SETTINGS_GET_OPTIONS, payload: options })
}

export const getPluginSettings = () => async (dispatch: TDispatch) => {
	dispatch({ type: SETTINGS_GET_SETTINGS + START })

	try {
		const { settings } = await getData('settings')
		dispatch({ type: SETTINGS_GET_SETTINGS + SUCCESS, payload: settings })
	} catch (error: unknown) {
		dispatch({ type: SETTINGS_GET_SETTINGS + ERROR, error })
	}
}

export const savePluginSettings =
	() =>
	async (
		dispatch: TDispatch,
		getState: () => IReducers,
		enqueueSnackbar: (message: SnackbarMessage, options?: OptionsObject) => SnackbarKey,
	): Promise<void> => {
		const { settings: { settings } = {}, locales: { data } = {} } = getState()
		const { messages: { settings: { savingSuccess = '', savingError = '' } = {} } = {} } = data ?? {}
		dispatch({ type: SETTINGS_SAVE_SETTINGS + START })

		try {
			const { success } = await postData('settings', JSON.stringify(settings))
			if (success) {
				dispatch({ type: SETTINGS_SAVE_SETTINGS + SUCCESS })
				enqueueSnackbar(savingSuccess, { variant: 'success' })
			} else {
				dispatch({ type: SETTINGS_SAVE_SETTINGS + ERROR, error: 'WTF' })
				enqueueSnackbar(savingError, { variant: 'error' })
			}
		} catch (error: unknown) {
			dispatch({ type: SETTINGS_SAVE_SETTINGS + ERROR, error })
			enqueueSnackbar(savingError, { variant: 'error' })
		}
	}
