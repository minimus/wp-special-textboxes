import React from 'react'
import SaveIcon from '@mui/icons-material/Save'
import { useDispatch, useSelector, useStore } from 'react-redux'
import { useSnackbar } from 'notistack'
import { Container, Root } from './styles'
import ImagesSettings from './components/ImagesSettings'
import BoxSettings from './components/BoxSettings'
import ShadowsSettings from './components/ShadowsSettings'
import TextSettings from './components/TextSettings'
import SystemSettings from './components/SystemSettings'
import DeactivationSettings from './components/DeactivationSettings'
import { TDispatch, ILocale } from '../../../types/admin'
import { SETTINGS_SAVE_SETTINGS, STOP } from '../../redux/constants'
import { ProgressFab } from '@minimus/simplelib-ui-kit'
import { IReducers } from '../../../types/state'
import { savePluginSettings } from '../../redux/modules/settings/actions'

const Settings = (): JSX.Element => {
	const dispatch: TDispatch = useDispatch()
	const store = useStore()

	const { enqueueSnackbar } = useSnackbar()

	const saving: boolean = useSelector((state: IReducers) => state.settings.saving)
	const savingSuccess: boolean = useSelector((state: IReducers) => state.settings.savingSuccess)
	const savingError: boolean = useSelector((state: IReducers) => state.settings.savingError)
	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)

	const { core: { saveTooltip = '' } = {} } = localesData ?? {}

	const onClick = (): void => {
		if (!saving) {
			// eslint-disable-next-line @typescript-eslint/unbound-method
			void savePluginSettings()(dispatch, store.getState as () => IReducers, enqueueSnackbar).then()
		}
	}

	const onDelay = () => {
		dispatch({ type: SETTINGS_SAVE_SETTINGS + STOP })
	}

	return (
		<Container>
			<Root>
				<BoxSettings />
				<ImagesSettings />
				<TextSettings />
				<ShadowsSettings />
				<SystemSettings />
				<DeactivationSettings />
			</Root>
			<ProgressFab
				processing={saving}
				success={savingSuccess}
				error={savingError}
				tooltip={saveTooltip}
				color="primary"
				displayPosition={{ bottom: 30, right: 40 }}
				delay={3000}
				onClick={onClick}
				onDelay={onDelay}
			>
				<SaveIcon />
			</ProgressFab>
		</Container>
	)
}

export default Settings
