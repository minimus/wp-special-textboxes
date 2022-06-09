import { HEADER_FETCH_DATA, ERROR, START, SUCCESS } from '../../constants'
import type { TAction, TSysInfo } from '../../../../types/admin'
import { IHeaderState } from '../../../../types/state'

const initialState: IHeaderState = {
	sysInfo: null,
	loading: false,
}

const reducer = (state: IHeaderState = initialState, action: TAction): IHeaderState => {
	switch (action.type) {
		case HEADER_FETCH_DATA + START:
			return { ...state, loading: true }

		case HEADER_FETCH_DATA + SUCCESS:
			return { ...state, sysInfo: { ...(action.payload as TSysInfo) }, loading: false }

		case HEADER_FETCH_DATA + ERROR:
			return { ...state, loading: false }

		default:
			return state
	}
}

export default reducer
