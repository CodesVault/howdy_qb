import React from 'react'
import { DocsThemeConfig } from 'nextra-theme-docs'
import { useRouter } from 'next/router'
import { useConfig } from 'nextra-theme-docs'

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
  },
  useNextSeoProps() {
    return {
      titleTemplate: '%s | WP QueryBuilder'
    }
  },
  head: () => {
	  const { asPath } = useRouter()
	  const { title } = useConfig()
	  return <>
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	  <meta property="og:url" content={`https://wp-querybuilder.pages.dev${asPath}`} />
      <meta property="og:title" content={`${title} | WP QueryBuilder` || 'WP QueryBuilder'} />
      <meta property="twitter:title" content={`${title} | WP QueryBuilder` || 'WP QueryBuilder'} />
      <meta property="og:description" content="Query builder for WordPress" />
      <meta property="twitter:description" content="Query builder for WordPress" />
	  <meta property="og:type" content="documentation" />
	  <meta property="twitter:card" content="documentation" />
	  <meta property='og:image' content='//abmsourav.com/welcome/wp-content/uploads/2023/01/WP-QueryBuilder.jpg' />
	  <meta property='twitter:image' content='//abmsourav.com/welcome/wp-content/uploads/2023/01/WP-QueryBuilder.jpg' />
    </>
  },
  gitTimestamp: null,
  banner: {
    key: '1.6.3-release',
    text: 'WP QueryBuilder 1.6.3 is released.',
  }
}

export default config
