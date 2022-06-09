import React, { ChangeEvent } from 'react'
import { TextField } from '@mui/material'
import { useSelector } from 'react-redux'
import EditorSection from './EditorSection'
import { EditorSectionContentRow, TrashInfo, TypeInfo } from '../styles'
import { ILocale } from '../../../../types/admin'
import { IReducers } from '../../../../types/state'

type TProps = {
	slug: string
	type: string
	caption: string
	trash: boolean | number
	slugIsValid: boolean
	onChange: (val: Record<string, unknown>) => void
}

const NamesSection = (props: TProps): JSX.Element => {
	const { slug, type, caption, trash, slugIsValid, onChange } = props

	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)
	const {
		editor: {
			namesSection: {
				caption: captionString = '',
				captionCaption = '',
				captionTooltip = '',
				nameCaption = '',
				nameTooltip = '',
				nameErrorTooltip = '',
				typeCaption = '',
				trashInTrash = '',
				trashActive = '',
			} = {},
		} = {},
	} = localesData ?? {}

	const onValueChange =
		(key: string) =>
		(event: ChangeEvent<HTMLInputElement>): void => {
			if (onChange) {
				const { value } = event.target
				onChange({ [key]: value })
			}
		}

	return (
		<EditorSection caption={captionString}>
			<EditorSectionContentRow style={{ padding: '5px 0' }}>
				<TextField
					id="box-caption"
					variant="outlined"
					value={caption}
					label={captionCaption}
					helperText={captionTooltip}
					fullWidth
					onChange={onValueChange('caption')}
				/>
			</EditorSectionContentRow>
			<EditorSectionContentRow style={{ padding: '5px 0' }}>
				<TextField
					id="box-name"
					variant="outlined"
					value={slug}
					label={nameCaption}
					helperText={slugIsValid ? nameTooltip : nameErrorTooltip}
					error={!slugIsValid}
					fullWidth
					InputProps={{ readOnly: type !== 'custom' || slug === 'custom' }}
					onChange={onValueChange('slug')}
				/>
			</EditorSectionContentRow>
			<TypeInfo>
				{typeCaption}: <span>{type}</span>
			</TypeInfo>
			<TrashInfo trash={trash}>{trash ? trashInTrash : trashActive}</TrashInfo>
		</EditorSection>
	)
}

export default NamesSection
