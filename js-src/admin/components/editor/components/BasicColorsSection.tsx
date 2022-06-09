import React from 'react'
import EditorSection from './EditorSection'
import { EditorSectionContentRow, EditorSectionRowContentLine } from '../styles'
import { ColorsInput } from '@minimus/simplelib-ui-kit'
import { FieldCaption, FieldTooltip } from '../../../../ui-kit/styles'
import { TBasicColorsSectionStrings } from '../../../../types/admin'

type TProps = {
	id: string
	title: string
	color: string
	background: string[]
	locale: TBasicColorsSectionStrings
	onChange: (val: Record<string, unknown>) => void
}

const BasicColorsSection = (props: TProps): JSX.Element => {
	const { id, title, background, color, locale, onChange } = props
	const [startColor, stopColor] = background
	const {
		gradientTitle = '',
		gradientStartCaption = '',
		gradientStopCaption = '',
		gradientTooltip = '',
		fontColorCaption = '',
	} = locale ?? {}

	const onValueChange =
		(key: string, index = -1) =>
		(value: string): void => {
			if (onChange) {
				switch (index) {
					case 0:
						onChange({ [key]: [value, stopColor] })
						break
					case 1:
						onChange({ [key]: [startColor, value] })
						break
					default:
						onChange({ [key]: value })
				}
			}
		}

	return (
		<EditorSection caption={title}>
			<EditorSectionContentRow style={{ flexWrap: 'wrap' }}>
				<FieldCaption>{gradientTitle}</FieldCaption>
				<EditorSectionRowContentLine count={2}>
					<ColorsInput
						id={`${id}-background-color`}
						value={startColor}
						caption={gradientStartCaption}
						size="small"
						inside
						onChange={onValueChange('background', 0)}
					/>
					<ColorsInput
						id={`${id}-background-color`}
						value={stopColor}
						caption={gradientStopCaption}
						size="small"
						inside
						onChange={onValueChange('background', 1)}
					/>
				</EditorSectionRowContentLine>
				<FieldTooltip>{gradientTooltip}</FieldTooltip>
			</EditorSectionContentRow>
			<EditorSectionContentRow>
				<ColorsInput
					id="font-color"
					value={color}
					caption={fontColorCaption}
					tooltip="This is the font color of contained text."
					inside
					onChange={onValueChange('color')}
				/>
			</EditorSectionContentRow>
		</EditorSection>
	)
}

export default BasicColorsSection
