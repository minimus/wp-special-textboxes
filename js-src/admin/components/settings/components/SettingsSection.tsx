import React from 'react'
import { SettingsSectionRoot, SettingsSectionBody, SettingsSectionCaption } from '../styles'

type TProps = {
	caption: string
	children: JSX.Element | JSX.Element[]
}

const SettingsSection = (props: TProps): JSX.Element => {
	const { caption, children } = props

	return (
		<SettingsSectionRoot>
			<SettingsSectionCaption>{caption}</SettingsSectionCaption>
			<SettingsSectionBody>{children}</SettingsSectionBody>
		</SettingsSectionRoot>
	)
}

export default SettingsSection
