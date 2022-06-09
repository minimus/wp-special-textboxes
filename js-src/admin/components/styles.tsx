import React from 'react'
import { createGlobalStyle } from 'styled-components'
import type { TSettings, TShadowSettings, TStyle, TStyleColors } from '../../types/admin'

type TGlobalColorsProps = {
	settings: TSettings
	styles: TStyle[]
}

type TColorsProps = {
	slug: string
	colors: TStyleColors
	borderStyle: string
}

type TCommonProps = {
	settings: TSettings
}

export const GlobalCoreStyles = createGlobalStyle`
  .stb-container {
    display: flex;
    overflow: hidden;
    box-sizing: border-box;
	padding: 0;

    .stb-caption {
      display: flex;
      flex-direction: column;
      order: 1;

      .stb-logo {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 60px;
        height: 60px;
        order: 1;

        &__image {
          width: 50px;
          height: 50px;
        }
      }

      .stb-caption-content {
        display: none;
        order: 2;
      }

      .stb-tool {
        display: none;
        order: 3;
      }
    }

    .stb-content {
      order: 2;
      width: 100%;

      p:first-child {
        margin-block-start: 0;
      }

      p:last-child {
        margin-block-end: 0;
      }
    }

    &.stb-image-small {
      .stb-caption {
        .stb-logo {
          width: 30px;
          height: 30px;

          &__image {
            width: 25px;
            height: 25px;
          }
        }
      }
    }

    &.stb-image-none {
      .stb-caption {
        display: none;
      }
    }
  }

  .stb-container.stb-caption-box {
    flex-direction: column;

    .stb-caption {
      display: flex;
      flex-direction: row;
      align-items: center;
      padding: 0 3px;

      .stb-logo {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 27px;
        height: 27px;
        order: 1;

        .stb-logo__image {
          width: 25px;
          height: 25px;
        }
      }

      .stb-caption-content {
        display: inherit;
        width: 100%;
        padding: 0 3px;
        order: 2;
      }

      .stb-tool {
        display: inherit;
        justify-self: flex-end;
        width: 27px;
        height: 27px;
        cursor: pointer;
        order: 3;
      }
    }

    .stb-content {
      /*max-height: 2000px;*/
      overflow: hidden;
      width: 100%;
      transition: all .3s linear;
      will-change: transform;

      p:first-child {
        margin-block-start: 0;
      }

      p:last-child {
        margin-block-end: 0;
      }
    }

    &.stb-fixed {
      .stb-caption {
        .stb-tool {
          display: none;
        }
      }
    }

    &.stb-collapsed {
      .stb-content {
        font-size: 0;
      }

      .stb-content img {
        width: 0;
        height: 0;
      }
    }
  }
  
  .stb-container.stb-widget {
	margin-left: 0;
	margin-right: 0;
	box-shadow: none;
  }
`

export const getMargins = (settings: TSettings): string =>
	`${settings.margins.top}px ${settings.margins.right}px ${settings.margins.bottom}px ${settings.margins.left}px`

export const getTextShadow = (settings: TSettings): string => {
	const { enabled, offsetX, offsetY, blur, color }: TShadowSettings = settings.text.shadow
	if (!enabled) return 'unset'
	return `${offsetX}px ${offsetY}px ${blur}px ${color}`
}

export const getBoxShadow = (settings: TSettings): string => {
	const { enabled, inset, offsetX, offsetY, blur, color }: TShadowSettings = settings.shadow
	if (!enabled) return 'unset'
	return `${inset ? 'inset ' : ''}${offsetX}px ${offsetY}px ${blur}px ${color}`
}

export const GlobalCommonStyles = createGlobalStyle`
  .stb-container {
    margin: ${(props: TCommonProps) => getMargins(props.settings)};
    border-radius: ${(props: TCommonProps) => (props.settings.roundedCorners ? props.settings.radius : 0)}px;
    box-shadow: ${(props: TCommonProps) => getBoxShadow(props.settings)};

    .stb-caption {
	  .stb-caption-content {
        font-size: ${(props: TCommonProps) =>
			props.settings.caption.font.fontSize === 0 ? 'unset' : `${props.settings.caption.font.fontSize}px`};
        font-family: ${(props: TCommonProps) =>
			props.settings.caption.font.fontFamily ? props.settings.caption.font.fontFamily : 'unset'};
      }
      .stb-tool {
        background-image: url(${(props: TCommonProps) =>
			props.settings.imgMinus.enabled ? props.settings.imgMinus.image : props.settings.imgMinus.defaultImage});
        background-position: center;
        background-color: transparent;
        background-repeat: no-repeat;
      }
    }

    .stb-content {
      padding: 10px;
      font-size: ${(props: TCommonProps) =>
			props.settings.text.font.fontSize === 0 ? 'unset' : `${props.settings.text.font.fontSize}px`};
      font-family: ${(props: TCommonProps) =>
			props.settings.text.font.fontFamily ? props.settings.text.font.fontFamily : 'unset'};
      text-shadow: ${(props: TCommonProps) => getTextShadow(props.settings)};
    }

    &.stb-collapsed {
      .stb-caption {
        .stb-tool {
          background-image: url(${(props: TCommonProps) =>
				props.settings.imgPlus.enabled ? props.settings.imgPlus.image : props.settings.imgPlus.defaultImage});
        }
      }

      .stb-content {
        padding-top: 0;
        padding-bottom: 0;
      }
    }

    &.stb-no-caption.stb-ltr:not(.stb-caption-box),
    &.stb-no-caption:not(.stb-caption-box) {
      direction: ltr;

      .stb-content {
        padding: 10px 10px 10px 0;
      }
    }

    &.stb-no-caption.stb-rtl:not(.stb-caption-box) {
      direction: rtl;

      .stb-content {
        padding: 10px 0 10px 10px;
      }
    }
  }
`

export const EditorColorStyles = createGlobalStyle`
  .stb-container.stb-style-${(props: TColorsProps) => props.slug} {
    color: ${(props: TColorsProps) => props.colors.body.color};
    border: ${(props: TColorsProps) => `1px ${props.borderStyle ?? 'solid'} ${props.colors.border.color}`};
    background-image: linear-gradient(to bottom,
    ${(props: TColorsProps) => props.colors.body.background[0]} 30%,
    ${(props: TColorsProps) => props.colors.body.background[1]} 90%);

    & .stb-caption {
      color: ${(props: TColorsProps) => props.colors.caption.color};
      background-image: linear-gradient(to bottom,
      ${(props: TColorsProps) => props.colors.caption.background[0]} 30%,
      ${(props: TColorsProps) => props.colors.caption.background[1]} 90%);
    }

    &.stb-no-caption:not(.stb-caption-box) .stb-caption {
      background-image: linear-gradient(to bottom,
      ${(props: TColorsProps) => props.colors.body.background[0]} 30%,
      ${(props: TColorsProps) => props.colors.body.background[1]} 90%);
    }
  }
`

export const GlobalColorStyles = (props: TGlobalColorsProps): JSX.Element => (
	<>
		{props.styles.map(
			(style: TStyle): JSX.Element => (
				<EditorColorStyles
					key={style.slug}
					slug={style.slug}
					colors={style.colors}
					borderStyle={props.settings.borderStyle}
				/>
			),
		)}
	</>
)
