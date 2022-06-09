import { START, STYLES_CHANGE_FILTER, STYLES_FETCH_DATA, STYLES_NEED_RELOAD, SUCCESS, ERROR } from '../../constants'
import type { TAction, TStyle } from '../../../../types/admin'
import { IStylesState } from '../../../../types/state'

const initialState: IStylesState = {
	styles: null,
	loading: false,
	needReload: true,
	filter: 1,
}

const reducer = (state: IStylesState = initialState, action: TAction): IStylesState => {
	switch (action.type) {
		case STYLES_FETCH_DATA + START:
			return { ...state, loading: true, needReload: false }

		case STYLES_FETCH_DATA + SUCCESS:
			return { ...state, styles: action.payload as TStyle[], loading: false }

		case STYLES_FETCH_DATA + ERROR:
			return { ...state, loading: false }

		case STYLES_CHANGE_FILTER:
			return { ...state, filter: action.payload as number }

		case STYLES_NEED_RELOAD:
			return { ...state, needReload: true }

		default:
			return state
	}
}

export default reducer
