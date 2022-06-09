import React from 'react'
import { useDispatch, useSelector } from 'react-redux'
import SettingsSection from './SettingsSection'
import { TDispatch, TFontSettings, ILocale, TSettings } from '../../../../types/admin'
import TextParams from '../../../../ui-kit/TextParams'
import { IReducers } from '../../../../types/state'
import { SETTINGS_SET_SETTINGS } from '../../../redux/constants'
import { RadioInput } from '@minimus/simplelib-ui-kit'

const TextSettings = (): JSX.Element => {
	const dispatch: TDispatch = useDispatch()

	const settings: TSettings = useSelector((state: IReducers) => state.settings.settings)
	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)

	const {
		caption: { font: captionFont },
		text: { font: textFont },
		caption,
		text,
		langDirect,
	} = settings

	const {
		core: { leftToRight = '', rightToLeft = '' } = {},
		settings: {
			textSettings: {
				caption: captionString = '',
				textFontCaption = '',
				textFontSizeLabel = '',
				textFontFamilyLabel = '',
				captionFontCaption = '',
				captionFontSizeLabel = '',
				captionFontFamilyLabel = '',
				fontSizeHelperText = '',
				fontFamilyHelperText = '',
				languageDirectionCaption = '',
				languageDirectionTooltip = '',
			} = {},
		} = {},
	} = localesData ?? {}

	const onChange =
		(key: 'text' | 'caption') =>
		(value: TFontSettings): void => {
			if (key === 'text') {
				dispatch({ type: SETTINGS_SET_SETTINGS, payload: { text: { ...text, font: value } } })
			} else if (key === 'caption') {
				dispatch({ type: SETTINGS_SET_SETTINGS, payload: { caption: { ...caption, font: value } } })
			}
		}

	const onRadioChange = (value: string) => {
		dispatch({ type: SETTINGS_SET_SETTINGS, payload: { langDirect: value } })
	}

	return (
		<SettingsSection caption={captionString}>
			<TextParams
				caption={textFontCaption}
				sizeLabel={textFontSizeLabel}
				sizeHelperText={fontSizeHelperText}
				familyLabel={textFontFamilyLabel}
				familyHelperText={fontFamilyHelperText}
				id="text"
				values={textFont}
				onChange={onChange('text')}
			/>
			<TextParams
				caption={captionFontCaption}
				sizeLabel={captionFontSizeLabel}
				sizeHelperText={fontSizeHelperText}
				familyLabel={captionFontFamilyLabel}
				familyHelperText={fontFamilyHelperText}
				id="caption"
				values={captionFont}
				onChange={onChange('caption')}
			/>
			<RadioInput
				id="langDirect"
				value={langDirect}
				values={[
					{ text: leftToRight ?? 'Left-to-Right', value: 'ltr' },
					{ text: rightToLeft ?? 'Right-to-Left', value: 'rtl' },
				]}
				caption={languageDirectionCaption}
				tooltip={languageDirectionTooltip}
				direction="row"
				onChange={onRadioChange}
			/>
		</SettingsSection>
	)
}

export default TextSettings
