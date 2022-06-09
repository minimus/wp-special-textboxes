import React from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { TDispatch, ILocale, TSettings } from '../../../../types/admin'
import { IReducers } from '../../../../types/state'
import SettingsSection from './SettingsSection'
import { RadioInput } from '@minimus/simplelib-ui-kit'
import { SETTINGS_SET_SETTINGS } from '../../../redux/constants'

const SystemSettings = (): JSX.Element => {
	const dispatch: TDispatch = useDispatch()

	const settings: TSettings = useSelector((state: IReducers) => state.settings.settings)
	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)

	const { cssLoading } = settings
	const {
		settings: {
			systemSettings: {
				caption: captionString = '',
				loadingCaption = '',
				staticCaption = '',
				clientDynamicCaption = '',
				serverDynamicCaption = '',
				staticTooltip = '',
				// clientDynamicTooltip = '',
				serverDynamicTooltip = '',
			} = {},
		} = {},
	} = localesData ?? {}

	const onChange = (value: string): void => {
		dispatch({ type: SETTINGS_SET_SETTINGS, payload: { cssLoading: value } })
	}

	return (
		<SettingsSection caption={captionString}>
			<RadioInput
				id="cssLoading"
				value={cssLoading}
				tooltip={
					<>
						<b>{staticCaption}</b> - {staticTooltip}
						{/* <br/>
							<b>{clientDynamicCaption}</b> - {clientDynamicTooltip} */}
						<br />
						<b>{serverDynamicCaption}</b> - {serverDynamicTooltip}
					</>
				}
				caption={loadingCaption}
				values={[
					{ text: staticCaption ?? 'Static', value: 'static' },
					// { text: clientDynamicCaption ?? 'Client Dynamic', value: 'styled' },
					{ text: serverDynamicCaption ?? 'Server Dynamic', value: 'dynamic' },
				]}
				direction="row"
				onChange={onChange}
			/>
		</SettingsSection>
	)
}

export default SystemSettings
