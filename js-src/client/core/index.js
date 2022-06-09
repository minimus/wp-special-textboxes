;(() => {
	const stbButtons = document.querySelectorAll('.stb-container .stb-caption .stb-tool')

	if (stbButtons.length) {
		;[...stbButtons].forEach((button) => {
			button.addEventListener('click', (event) => {
				const container = event.target?.parentNode?.parentNode
				container.classList.toggle('stb-collapsed')
			})
		})
	}
})()
