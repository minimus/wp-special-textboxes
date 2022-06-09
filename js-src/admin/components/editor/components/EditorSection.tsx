import React from 'react'
import { EditorSectionBody, EditorSectionCaption, EditorSectionRoot } from '../styles'

type TProps = {
	caption: string
	children: JSX.Element | JSX.Element[]
}

const EditorSection = (props: TProps): JSX.Element => {
	const { caption, children } = props

	return (
		<EditorSectionRoot>
			<EditorSectionCaption>{caption}</EditorSectionCaption>
			<EditorSectionBody>{children}</EditorSectionBody>
		</EditorSectionRoot>
	)
}

export default EditorSection
