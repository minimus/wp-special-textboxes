import type { TAction, TThemeInfo } from '../../../../types/admin'
import { THEMES_ACTIVATE_THEME, THEMES_LOADING_THEMES_INFO, ERROR, START, STOP, SUCCESS } from '../../constants'
import { IThemesState } from '../../../../types/state'

const initialState: IThemesState = {
	themes: null,
	loading: false,
	loaded: false,
	activatingSlug: '',
	activated: false,
	activationError: false,
}

const themes = (state: IThemesState = initialState, action: TAction): IThemesState => {
	switch (action.type) {
		case THEMES_LOADING_THEMES_INFO + START:
			return { ...state, loading: true, loaded: false }

		case THEMES_LOADING_THEMES_INFO + SUCCESS:
			return { ...state, themes: action.payload as TThemeInfo[], loaded: true, loading: false }

		case THEMES_LOADING_THEMES_INFO + ERROR:
			return { ...state, loading: false, loaded: false, themes: null }

		case THEMES_ACTIVATE_THEME + START:
			return { ...state, loading: true, activatingSlug: action.payload as string }

		case THEMES_ACTIVATE_THEME + ERROR:
			return { ...state, loading: false, activated: false, activationError: true }

		case THEMES_ACTIVATE_THEME + STOP:
			return { ...state, activatingSlug: '', loading: false, activationError: false, activated: false }

		default:
			return state
	}
}

export default themes
