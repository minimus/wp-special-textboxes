import { createStore, applyMiddleware, combineReducers, Reducer, ReducersMapObject, CombinedState } from 'redux'
import { composeWithDevTools } from 'redux-devtools-extension'
import thunk from 'redux-thunk'
import header from './modules/header/header'
import settings from './modules/settings/settings'
import styles from './modules/styles/styles'
import editor from './modules/editor/editor'
import locales from './modules/locales/locales'
import themes from './modules/themes/themes'

declare const __DEV__: string | undefined

const createStoreWithMiddleware = __DEV__
	? composeWithDevTools(applyMiddleware(thunk))(createStore)
	: applyMiddleware(thunk)(createStore)

const reducer: Reducer<CombinedState<Record<string, unknown>>> = combineReducers({
	settings,
	styles,
	editor,
	themes,
	header,
	locales,
} as ReducersMapObject)

export default (initialState: Record<string, unknown>) => createStoreWithMiddleware(reducer, initialState)
