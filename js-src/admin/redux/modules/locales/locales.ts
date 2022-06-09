import type { TAction, ILocale } from '../../../../types/admin'
import { LOCALIZATION_GET_STRINGS, ERROR, START, SUCCESS } from '../../constants'
import { ILocaleState } from '../../../../types/state'

const initialState: ILocaleState = {
	data: null,
	loading: false,
	loaded: false,
}

const locales = (state: ILocaleState = initialState, action: TAction): ILocaleState => {
	switch (action.type) {
		case LOCALIZATION_GET_STRINGS + START:
			return { ...state, loading: true, loaded: false }

		case LOCALIZATION_GET_STRINGS + SUCCESS:
			return { ...state, data: action.payload as ILocale, loading: false, loaded: true }

		case LOCALIZATION_GET_STRINGS + ERROR:
			return { ...state, loading: false, loaded: false }

		default:
			return state
	}
}

export default locales
