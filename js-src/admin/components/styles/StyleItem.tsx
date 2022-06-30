import React from 'react'
import IconButton from '@mui/material/IconButton'
import EditIcon from '@mui/icons-material/Edit'
import DeleteIcon from '@mui/icons-material/Delete'
import DeleteForeverIcon from '@mui/icons-material/DeleteForever'
import UndoIcon from '@mui/icons-material/Undo'
import { useNavigate } from 'react-router-dom'
import { useDispatch } from 'react-redux'
import type { TColors, TDispatch, TStyleColors } from '../../../types/admin'
import { ItemAlias, ItemBody, ItemContent, ItemInfo, ItemToolbar } from './styless'
import StyleBox from '../box/StyleBox'
import { STYLES_NEED_RELOAD } from '../../redux/constants'
import { deleteStyleFromTrash, setStyleTrash } from '../../redux/modules/styles/actions'

/* const useStyles = makeStyles((theme) => ({
    button: {
        margin: theme.spacing(1),
    },
    input: {
        display: 'none',
    },
})) */

type TProps = {
	caption: string
	slug: string
	type: string
	colors: TStyleColors | TColors
	trash: 0 | 1
	captionBg?: boolean
	text: string[]
}

const StyleItem = (props: TProps): JSX.Element => {
	const dispatch: TDispatch = useDispatch()

	const { caption, slug, type, colors, trash, captionBg = false, text = [] } = props
	// const buttonClasses = useStyles()
	const { color } = colors.body

	const [aliasString = '', typeString = ''] = text

	const navigate = useNavigate()

	const onEditorClick = (): void => {
		navigate(`/editor/${slug}`)
	}

	const onDeleteClick = (): void => {
		void setStyleTrash(
			slug,
			{ slug, type, caption, colors, trash },
			1,
		)(dispatch).then(() => {
			dispatch({ type: STYLES_NEED_RELOAD })
		})
	}

	const onUndoClick = (): void => {
		void setStyleTrash(
			slug,
			{ slug, type, caption, colors, trash },
			0,
		)(dispatch).then(() => {
			dispatch({ type: STYLES_NEED_RELOAD })
		})
	}

	const onKillClick = (): void => {
		void deleteStyleFromTrash(slug)(dispatch).then()
	}

	const deleteButtons = (): JSX.Element => {
		if (trash) {
			return (
				<>
					<IconButton aria-label="delete-forever" size="small" style={{ color }} onClick={onKillClick}>
						<DeleteForeverIcon fontSize="inherit" />
					</IconButton>
					<IconButton aria-label="undo" size="small" style={{ color }} onClick={onUndoClick}>
						<UndoIcon fontSize="inherit" />
					</IconButton>
				</>
			)
		}
		return (
			<IconButton aria-label="delete" size="small" style={{ color }} onClick={onDeleteClick}>
				<DeleteIcon fontSize="inherit" />
			</IconButton>
		)
	}

	return (
		<StyleBox caption={caption} slug={slug} colors={colors} captionBg={captionBg}>
			<ItemContent>
				<ItemBody>
					<p style={{ fontSize: '1.3em', fontWeight: 600 }}>{caption}</p>
					<ItemAlias>{`${aliasString}: ${slug}`}</ItemAlias>
					<ItemInfo>{`${typeString}: ${type}`}</ItemInfo>
				</ItemBody>
				<ItemToolbar>
					<IconButton aria-label="delete" size="small" style={{ color }} onClick={onEditorClick}>
						<EditIcon fontSize="inherit" />
					</IconButton>
					{type === 'custom' && deleteButtons()}
				</ItemToolbar>
			</ItemContent>
		</StyleBox>
	)
}

StyleItem.defaultProps = {
	captionBg: false,
}

export default StyleItem
