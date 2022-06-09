import React from 'react'
import { useDispatch, useSelector, useStore } from 'react-redux'
import SaveIcon from '@mui/icons-material/Save'
import { useNavigate, useParams } from 'react-router-dom'
import { useSnackbar } from 'notistack'
import { Root } from './styles'
import type { TDispatch, TEventValue, ILocale, TSettings, TStyle } from '../../../types/admin'
import type { IReducers } from '../../../types/state'
import StyleBox from '../box/StyleBox'
import { EditorColorStyles, GlobalCommonStyles, GlobalCoreStyles } from '../styles'
import BasicColorsSection from './components/BasicColorsSection'
import BorderImageSection from './components/BorderImageSection'
import NamesSection from './components/NamesSection'
import {
	EDITOR_SET_BODY,
	EDITOR_SET_STYLE,
	EDITOR_SET_CAPTION,
	EDITOR_SET_IMAGE,
	EDITOR_SET_BORDER,
	EDITOR_POST_DATA,
	STOP,
} from '../../redux/constants'
import { ProgressFab } from '@minimus/simplelib-ui-kit'
import { saveEditorData } from '../../redux/modules/editor/actions'

type TProps = {
	newStyle?: boolean
}

const Editor = (props: TProps): JSX.Element => {
	const dispatch: TDispatch = useDispatch()
	const store = useStore()
	const { enqueueSnackbar } = useSnackbar()
	const navigate = useNavigate()

	const { slug: actionSlug } = useParams()
	const { newStyle } = props

	const settings: TSettings = useSelector((state: IReducers) => state.settings.settings)
	const style: TStyle = useSelector((state: IReducers) => state.editor.style)
	const saving: boolean = useSelector((state: IReducers) => state.editor.saving)
	const savingSuccess: boolean = useSelector((state: IReducers) => state.editor.savingSuccess)
	const savingError: boolean = useSelector((state: IReducers) => state.editor.savingError)
	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)

	const { colors, caption: captionText, slug, type, trash } = style

	const { body, caption, border, image } = colors

	const {
		core: { saveTooltip = '' } = {},
		editor: { colorsSection: { bodyTitle = '', captionTitle = '', basicColorsSection = undefined } = {} } = {},
	} = localesData ?? {}

	const onValueChange =
		(actionType: string) =>
		(value: TEventValue): void => {
			dispatch({ type: actionType, payload: value })
		}

	const onSaveColors = () => {
		// eslint-disable-next-line @typescript-eslint/unbound-method
		void saveEditorData(actionSlug)(dispatch, store.getState as () => IReducers, enqueueSnackbar).then(() => {
			if (newStyle) {
				navigate(slug, { replace: true })
			}
		})
	}

	const onDelay = () => {
		dispatch({ type: EDITOR_POST_DATA + STOP })
	}

	const slugIsValid = /[a-zA-Z0-9-_]+/.test(slug)

	return (
		<Root>
			<GlobalCoreStyles />
			<GlobalCommonStyles settings={settings} />
			<EditorColorStyles slug={slug} colors={colors} borderStyle={settings.borderStyle} />
			<NamesSection
				slug={slug}
				type={type}
				caption={captionText}
				trash={trash}
				slugIsValid={slugIsValid}
				onChange={onValueChange(EDITOR_SET_STYLE)}
			/>
			<BorderImageSection
				border={border}
				image={image}
				onBorderChange={onValueChange(EDITOR_SET_BORDER)}
				onImageChange={onValueChange(EDITOR_SET_IMAGE)}
			/>
			<BasicColorsSection
				id="body"
				title={bodyTitle}
				color={body?.color}
				background={body?.background}
				locale={basicColorsSection}
				onChange={onValueChange(EDITOR_SET_BODY)}
			/>
			<BasicColorsSection
				id="caption"
				title={captionTitle}
				color={caption?.color}
				background={caption?.background}
				locale={basicColorsSection}
				onChange={onValueChange(EDITOR_SET_CAPTION)}
			/>
			<div>
				<StyleBox slug={slug} caption={captionText} colors={colors} captionBg={!!settings.side}>
					Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean nec urna sit amet nunc mattis
					volutpat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus a lectus lobortis,
					convallis urna sollicitudin, rhoncus justo. Ut pulvinar convallis tortor nec facilisis. Etiam mi
					leo, tempus vitae consectetur non, feugiat auctor nunc. Praesent efficitur, ante at posuere
					tristique, erat turpis imperdiet lectus, a pretium diam sapien ac nibh. Sed felis dolor, vestibulum
					non est a, commodo vulputate risus. Nunc et ligula mauris. Curabitur suscipit in nunc sed ultricies.
					Sed non varius risus. Praesent eget sollicitudin dolor. Integer ut efficitur libero. Mauris iaculis
					nec leo vel tempor.
				</StyleBox>
			</div>
			<div>
				<StyleBox slug={slug} caption={captionText} colors={colors} captioned>
					Quisque consectetur tortor nec felis aliquet, quis tristique elit eleifend. Etiam quam sem, vehicula
					et dictum nec, pellentesque scelerisque nulla. Nullam sed felis metus. Integer tempor laoreet
					aliquam. Nam eu sagittis dolor. Duis molestie dolor vitae cursus faucibus. Curabitur gravida laoreet
					ullamcorper. Etiam consectetur sapien at sagittis euismod. Nunc pulvinar blandit nisl ultrices
					ullamcorper.
				</StyleBox>
			</div>
			<ProgressFab
				processing={saving}
				success={savingSuccess}
				error={savingError}
				tooltip={saveTooltip}
				color="primary"
				displayPosition={{ bottom: 30, right: 40 }}
				delay={3000}
				onClick={onSaveColors}
				onDelay={onDelay}
			>
				<SaveIcon />
			</ProgressFab>
		</Root>
	)
}

Editor.defaultProps = {
	newStyle: false,
}

export default Editor
