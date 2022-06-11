import { registerBlockType } from '@wordpress/blocks'
import Edit from './edit'
import save from './save'
import metadata from './block.json'
import './editor.scss'

const {
	stbBlockSettings: {
		strings: {
			blockHeader = 'Special Text',
			blockDescription = 'Highlights block of text as colored text block.',
		} = {},
	} = {},
} = window ?? {}

registerBlockType(metadata.name, {
	title: blockHeader,
	description: blockDescription,
	edit: Edit,
	save,
})
