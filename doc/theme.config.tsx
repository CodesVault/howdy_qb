import React from 'react'
import { DocsThemeConfig } from 'nextra-theme-docs'

const config: DocsThemeConfig = {
  logo: <strong style={{ color: '#782cfd' }}>WP QueryBuilder</strong>,
  project: {
    link: 'https://github.com/CodesVault/howdy_qb',
  },
  docsRepositoryBase: 'https://github.com/CodesVault/howdy_qb/tree/doc/doc',
  footer: {
    text: <span>Copyright © 2022–2023 <a href="https://github.com/CodesVault" target="_blank">CodesVault</a></span>,
  },
  primaryHue: 260,
  sidebar: {
	defaultMenuCollapseLevel: 1
  }
//   banner: {
//     key: '2.0-release',
//     text: <a href="https://nextra.site" target="_blank">
//       🎉 Nextra 2.0 is released. Read more →
//     </a>,
//   },
}

export default config
