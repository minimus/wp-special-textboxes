import React from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { TDispatch, ILocale, TSettings } from '../../../../types/admin'
import SettingsSection from './SettingsSection'
import { CrossTextInput, SwitchInput } from '@minimus/simplelib-ui-kit'
import { IReducers } from '../../../../types/state'
import { SETTINGS_SET_SETTINGS } from '../../../redux/constants'
import BorderSettings from '../../../../ui-kit/BorderSettings'

const BoxSettings = (): JSX.Element => {
	const dispatch: TDispatch = useDispatch()

	const settings: TSettings = useSelector((state: IReducers) => state.settings.settings)
	const { margins, collapsing, collapsed } = settings ?? {}

	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)
	const {
		settings: {
			boxSettings: {
				boxSettingsCaption = '',
				marginsCaption = '',
				marginsCaptions = [],
				marginsTooltip = '',
				collapsingCaption = '',
				collapsedCaption = '',
			} = {},
		} = {},
	} = localesData ?? {}

	const onMarginsChange = (values: number[]): void => {
		const [top, left, right, bottom] = values
		dispatch({ type: SETTINGS_SET_SETTINGS, payload: { margins: { top, left, right, bottom } } })
	}

	const onCollapsingChange = (value: boolean): void => {
		dispatch({ type: SETTINGS_SET_SETTINGS, payload: { collapsing: value } })
	}

	const onCollapsedChange = (value: boolean): void => {
		dispatch({ type: SETTINGS_SET_SETTINGS, payload: { collapsed: value } })
	}

	return (
		<SettingsSection caption={boxSettingsCaption}>
			<CrossTextInput
				id="margins"
				caption={marginsCaption}
				captions={marginsCaptions}
				values={[margins?.top, margins?.left, margins?.right, margins?.bottom]}
				tooltip={marginsTooltip}
				onChange={onMarginsChange}
			/>
			<BorderSettings />
			<SwitchInput id="collapsing" value={collapsing} caption={collapsingCaption} onChange={onCollapsingChange} />
			<SwitchInput id="collapsed" value={collapsed} caption={collapsedCaption} onChange={onCollapsedChange} />
		</SettingsSection>
	)
}

export default BoxSettings
