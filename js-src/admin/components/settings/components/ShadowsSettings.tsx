import React from 'react'
import { useDispatch, useSelector } from 'react-redux'
import SettingsSection from './SettingsSection'
import ShadowSettings from '../../../../ui-kit/ShadowSettings'
import { TDispatch, ILocale, TSettings, TShadowSettings } from '../../../../types/admin'
import { IReducers } from '../../../../types/state'
import { SETTINGS_SET_SETTINGS } from '../../../redux/constants'

const ShadowsSettings = (): JSX.Element => {
	const dispatch: TDispatch = useDispatch()

	const settings: TSettings = useSelector((state: IReducers) => state.settings.settings)
	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)

	const {
		shadow,
		text: { shadow: textShadow },
		text,
	} = settings ?? {}

	const {
		settings: {
			shadowsSettings: {
				caption: captionString = '',
				boxShadowCaption = '',
				boxShadowLabel = '',
				textShadowCaption = '',
				textShadowLabel = '',
				shadowSettings = undefined,
			} = {},
		} = {},
	} = localesData ?? {}

	const onBoxShadowChange = (value: TShadowSettings): any => {
		dispatch({ type: SETTINGS_SET_SETTINGS, payload: { shadow: value } })
	}

	const onTextShadowChange = (value: TShadowSettings): any => {
		dispatch({ type: SETTINGS_SET_SETTINGS, payload: { text: { ...text, shadow: { ...value } } } })
	}

	return (
		<SettingsSection caption={captionString}>
			<ShadowSettings
				id="box-shadow"
				caption={boxShadowCaption}
				label={boxShadowLabel}
				locale={shadowSettings}
				shadow={shadow}
				onChange={onBoxShadowChange}
			/>
			<ShadowSettings
				id="text-shadow"
				caption={textShadowCaption}
				label={textShadowLabel}
				locale={shadowSettings}
				shadow={textShadow}
				variant="text"
				onChange={onTextShadowChange}
			/>
		</SettingsSection>
	)
}

export default ShadowsSettings
