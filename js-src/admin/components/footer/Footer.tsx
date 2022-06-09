import React from 'react'
import { useSelector } from 'react-redux'
import { FooterRoot } from './styles'
import type { ILocale } from '../../../types/admin'
import type { IReducers } from '../../../types/state'

const Footer = (): JSX.Element => {
	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)
	const { footer } = localesData ?? {}

	const currYear = new Date().getFullYear()
	const footerText = `Special Text Boxes for Wordpress. Copyright © 2010 - ${currYear}, `
	const footerText2 = footer?.rights
	const author = 'minimus'
	return (
		<FooterRoot>
			<span className="copyright-container">
				{footerText}
				<a href="https://www.simplelib.com/" target="_blank" rel="noopener noreferrer">
					{author}
				</a>
				. {footerText2}
			</span>
		</FooterRoot>
	)
}

export default Footer
