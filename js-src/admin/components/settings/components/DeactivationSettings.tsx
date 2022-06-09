import React from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { TDispatch, ILocale, TSettings } from '../../../../types/admin'
import { IReducers } from '../../../../types/state'
import SettingsSection from './SettingsSection'
import { SwitchInput } from '@minimus/simplelib-ui-kit'
import { FieldCaption } from '../../../../ui-kit/styles'
import { SETTINGS_SET_SETTINGS } from '../../../redux/constants'

const DeactivationSettings = (): JSX.Element => {
	const dispatch: TDispatch = useDispatch()

	const settings: TSettings = useSelector((state: IReducers) => state.settings.settings)
	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)

	const { deleteOptions, deleteDB } = settings
	const {
		settings: {
			deactivationSettings: {
				caption: captionString = '',
				actionsString = '',
				optionsCaption = '',
				dbCaption = '',
			} = {},
		} = {},
	} = localesData ?? {}

	const onChange =
		(key: string) =>
		(value: boolean): void => {
			dispatch({ type: SETTINGS_SET_SETTINGS, payload: { [key]: value } })
		}

	return (
		<SettingsSection caption={captionString}>
			<FieldCaption style={{ margin: '0 5px' }}>{actionsString}</FieldCaption>
			<SwitchInput
				id="deleteOptions"
				value={deleteOptions}
				caption={optionsCaption}
				onChange={onChange('deleteOptions')}
			/>
			<SwitchInput id="deleteDB" value={deleteDB} caption={dbCaption} onChange={onChange('deleteDB')} />
		</SettingsSection>
	)
}

export default DeactivationSettings
