import React, { useEffect } from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { useParams } from 'react-router-dom'
import type { TDispatch, TStyle } from '../../../types/admin'
import type { IReducers } from '../../../types/state'
import { getEditorData } from '../../redux/modules/editor/actions'
import Editor from '../../components/editor/Editor'

type TProps = {
	newStyle?: boolean
}

const EditorContainer = (props: TProps): JSX.Element => {
	const { newStyle = false } = props

	const dispatch: TDispatch = useDispatch()

	const { slug } = useParams()

	const style: TStyle = useSelector((state: IReducers) => state.editor.style)

	useEffect(() => {
		// if (!newStyle) {
		getEditorData(slug)(dispatch).then()
		// }
	}, [slug])

	if (!style) return null

	return <Editor newStyle={newStyle} />
}

EditorContainer.defaultProps = {
	newStyle: false,
}

export default EditorContainer
