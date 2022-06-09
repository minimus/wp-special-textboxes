import React from 'react'
import { FieldCaption, FieldTooltip, Root } from './styles'
import { TextInput } from '@minimus/simplelib-ui-kit'

type TProps = {
	caption: string
	sizeLabel: string
	sizeHelperText?: string
	familyLabel: string
	familyHelperText?: string
	tooltip?: string
	id: string
	values: {
		fontSize: number
		fontFamily: string
	}
	onChange?: (value: Record<string, string | number>) => void
}

const TextParams = (props: TProps): JSX.Element => {
	const {
		id,
		values,
		caption,
		sizeLabel,
		sizeHelperText = '',
		familyLabel,
		familyHelperText = '',
		tooltip = '',
		onChange,
	} = props
	const { fontSize, fontFamily } = values

	const handleChange = (key: string) => (value: string | number) => {
		if (onChange) {
			onChange({ fontSize, fontFamily, [key]: value })
		}
	}

	return (
		<Root size="fill">
			<FieldCaption style={{ margin: '0 5px 5px' }}>{caption}</FieldCaption>
			<TextInput
				id={`${id}-font-size`}
				value={fontSize}
				caption={sizeLabel}
				tooltip={sizeHelperText}
				suffix="px"
				contentType="number"
				size="small"
				onChange={handleChange('fontSize')}
			/>
			<TextInput
				id={`${id}-font-family`}
				value={fontFamily}
				caption={familyLabel}
				tooltip={familyHelperText}
				size="fill"
				onChange={handleChange('fontFamily')}
			/>
			{tooltip && <FieldTooltip style={{ margin: '0 5px' }}>{tooltip}</FieldTooltip>}
		</Root>
	)
}

TextParams.defaultProps = {
	sizeHelperText: '',
	familyHelperText: '',
	tooltip: '',
	onChange: undefined,
}

export default TextParams
