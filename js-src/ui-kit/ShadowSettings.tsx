import React from 'react'
import type { TShadowSettings, TShadowSettingsStrings } from '../types/admin'
import {
	FieldCaption,
	FieldTooltip,
	Root,
	ShadowSettingsContentRoot,
	ShadowSettingsContentRow,
	ShadowSettingsPreview,
	ShadowSettingsPreviewRoot,
	ShadowSettingsRoot,
} from './styles'
import { SwitchInput, TextInput, ColorsInput } from '@minimus/simplelib-ui-kit'

type TProps = {
	id: string
	shadow: TShadowSettings
	caption: string
	label: string
	tooltip?: string
	locale: TShadowSettingsStrings
	variant?: 'box' | 'text'
	onChange?: (TShadowSettings) => unknown
}

const ShadowSettings = (props: TProps): JSX.Element => {
	const { id, shadow, caption, label, tooltip, locale, variant = 'box', onChange } = props
	const { enabled = false, inset = false, offsetX = 0, offsetY = 0, blur = 0, color = '#000' } = shadow ?? {}
	const {
		insideShadow = '',
		offsetX: offsetXString = '',
		offsetY: offsetYString = '',
		blur: blurString = '',
		color: colorString = '',
		shadow: shadowString = '',
		preview: previewString = '',
	} = locale ?? {}

	const handleChange =
		(key: string) =>
		(value: unknown): void => {
			if (onChange && value !== undefined) {
				const result = { enabled, inset, offsetX, offsetY, blur, color, [key]: value }
				onChange(result)
			}
		}

	const getShadow = (): string =>
		enabled
			? `${inset && variant === 'box' ? 'inset ' : ''}${offsetX ?? 0}px ${offsetY ?? 0}px ${blur ?? 0}px ${color}`
			: 'unset'

	const style = {
		...(variant === 'box' ? { boxShadow: getShadow() } : { textShadow: getShadow() }),
	}

	return (
		<Root size="fill" id={id}>
			<FieldCaption style={{ margin: '0 5px' }}>{caption}</FieldCaption>
			<ShadowSettingsRoot>
				<ShadowSettingsContentRoot>
					<ShadowSettingsContentRow>
						<SwitchInput
							id={`${id}-enabled`}
							value={enabled}
							caption={label}
							onChange={handleChange('enabled')}
						/>
					</ShadowSettingsContentRow>
					{variant === 'box' && (
						<ShadowSettingsContentRow>
							<SwitchInput
								id={`${id}-inset`}
								value={inset}
								disabled={!enabled}
								caption={insideShadow}
								onChange={handleChange('inset')}
							/>
						</ShadowSettingsContentRow>
					)}
					<ShadowSettingsContentRow>
						<TextInput
							id={`${id}-offsetX`}
							value={offsetX}
							disabled={!enabled}
							caption={offsetXString}
							contentType="number"
							suffix="px"
							size="xSmall"
							inside
							onChange={handleChange('offsetX')}
						/>
						<TextInput
							id={`${id}-offsetY`}
							value={offsetY}
							disabled={!enabled}
							caption={offsetYString}
							contentType="number"
							suffix="px"
							size="xSmall"
							inside
							onChange={handleChange('offsetY')}
						/>
						<TextInput
							id={`${id}-blur`}
							value={blur}
							disabled={!enabled}
							caption={blurString}
							contentType="number"
							suffix="px"
							size="xSmall"
							inside
							onChange={handleChange('blur')}
						/>
					</ShadowSettingsContentRow>
					<ShadowSettingsContentRow>
						<ColorsInput
							id={`${id}-color`}
							value={color}
							disabled={!enabled}
							caption={colorString}
							size="small"
							inside
							onChange={handleChange('color')}
						/>
					</ShadowSettingsContentRow>
				</ShadowSettingsContentRoot>
				<ShadowSettingsPreviewRoot>
					<ShadowSettingsPreview style={style}>
						<span>{shadowString}</span>
						<span>{previewString}</span>
					</ShadowSettingsPreview>
				</ShadowSettingsPreviewRoot>
			</ShadowSettingsRoot>
			{tooltip && <FieldTooltip style={{ margin: '0 5px' }}>{tooltip}</FieldTooltip>}
		</Root>
	)
}

ShadowSettings.defaultProps = {
	tooltip: '',
	variant: 'box',
	onChange: undefined,
}

export default ShadowSettings
