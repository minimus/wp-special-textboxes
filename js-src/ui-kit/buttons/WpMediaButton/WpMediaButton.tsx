import React, { useRef } from 'react'
import IconButton from '@mui/material/IconButton'
import SearchIcon from '@mui/icons-material/Search'
import type { IWpMedia, TWpMediaAttachment } from '../../../types/admin'

type TProps = {
	disabled?: boolean
	onSelect?: (string) => void
}

interface IWindowWithMedia extends Window {
	wp?: {
		media: (m: Record<string, unknown>) => IWpMedia
	}
}

const WpMediaButton = (props: TProps): JSX.Element => {
	const { disabled, onSelect } = props

	const mediaRef = useRef(
		(window as IWindowWithMedia)?.wp?.media({
			/* title: mediaTexts.title,
            button: {
                text: mediaTexts.button,
            }, */
			multiple: false,
			state: 'library',
			library: {
				type: 'image',
			},
		}) ?? null,
	)

	const handleClick = (): void => {
		if (onSelect) {
			if (!mediaRef.current) {
				mediaRef.current = (window as IWindowWithMedia)?.wp?.media({
					multiple: false,
					state: 'library',
					library: {
						type: 'image',
					},
				})
			}

			mediaRef.current
				.on('select', () => {
					const attachment: TWpMediaAttachment = mediaRef.current.state().get('selection').first().toJSON()
					const { url } = attachment
					onSelect(url)
				})
				.open()
		}
	}

	return (
		<IconButton color="primary" size="medium" disabled={disabled} onClick={handleClick}>
			<SearchIcon />
		</IconButton>
	)
}

WpMediaButton.defaultProps = {
	disabled: false,
	onSelect: undefined,
}

export default WpMediaButton
