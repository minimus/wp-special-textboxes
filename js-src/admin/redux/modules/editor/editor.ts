import type { TAction, TStyle } from '../../../../types/admin'
import {
	EDITOR_CHANGE_DATA,
	EDITOR_GET_DATA,
	EDITOR_POST_DATA,
	EDITOR_SET_BODY,
	EDITOR_SET_BORDER,
	EDITOR_SET_CAPTION,
	EDITOR_SET_IMAGE,
	EDITOR_SET_STYLE,
	ERROR,
	START,
	STOP,
	SUCCESS,
} from '../../constants'
import { IEditorState } from '../../../../types/state'

const initialState: IEditorState = {
	style: null,
	loading: false,
	saving: false,
	savingSuccess: false,
	savingError: false,
}

const editor = (state: IEditorState = initialState, action: TAction): IEditorState => {
	switch (action.type) {
		case EDITOR_GET_DATA + START:
			return { ...state, loading: true }

		case EDITOR_GET_DATA + SUCCESS:
			return { ...state, loading: false, style: action.payload as TStyle }

		case EDITOR_GET_DATA + ERROR:
			return { ...state, loading: false }

		case EDITOR_POST_DATA + START:
			return { ...state, saving: true, savingError: false, savingSuccess: false }

		case EDITOR_POST_DATA + SUCCESS:
			return { ...state, saving: false, savingSuccess: true, savingError: false }

		case EDITOR_POST_DATA + ERROR:
			return { ...state, saving: false, savingSuccess: false, savingError: true }

		case EDITOR_POST_DATA + STOP:
			return { ...state, saving: false, savingSuccess: false, savingError: false }

		case EDITOR_CHANGE_DATA:
			return { ...state, ...(action.payload as Record<string, unknown>) }

		case EDITOR_SET_STYLE:
			return { ...state, style: { ...state.style, ...(action.payload as Record<string, unknown>) } }

		case EDITOR_SET_BODY:
			return {
				...state,
				style: {
					...state.style,
					colors: {
						...state.style.colors,
						body: { ...state.style.colors.body, ...(action.payload as Record<string, unknown>) },
					},
				},
			}

		case EDITOR_SET_CAPTION:
			return {
				...state,
				style: {
					...state.style,
					colors: {
						...state.style.colors,
						caption: { ...state.style.colors.caption, ...(action.payload as Record<string, unknown>) },
					},
				},
			}

		case EDITOR_SET_BORDER:
			return {
				...state,
				style: {
					...state.style,
					colors: {
						...state.style.colors,
						border: { ...state.style.colors.border, ...(action.payload as Record<string, unknown>) },
					},
				},
			}

		case EDITOR_SET_IMAGE:
			return {
				...state,
				style: {
					...state.style,
					colors: {
						...state.style.colors,
						image: { ...state.style.colors.image, ...(action.payload as Record<string, unknown>) },
					},
				},
			}

		default:
			return state
	}
}

export default editor
