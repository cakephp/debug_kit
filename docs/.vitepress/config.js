import baseConfig from '@cakephp/docs-skeleton/config'
import { createRequire } from 'module'

const require = createRequire(import.meta.url)
const tocEn = require('./toc_en.json')
const tocFr = require('./toc_fr.json')
const tocJa = require('./toc_ja.json')
const tocPt = require('./toc_pt.json')

const versions = {
  text: '5.x',
  items: [
    { text: '5.x (current)', link: 'https://book.cakephp.org/debugkit/5/', target: '_self' },
    { text: '4.x', link: 'https://book.cakephp.org/debugkit/4/en/', target: '_self' },
  ],
}

export default {
  extends: baseConfig,
  srcDir: '.',
  title: 'DebugKit',
  description: 'CakePHP DebugKit Documentation',
  base: '/debugkit/5/',
  rewrites: {
    'en/:slug*': ':slug*',
  },
  sitemap: {
    hostname: 'https://book.cakephp.org/debugkit/5/',
  },
  themeConfig: {
    siteTitle: false,
    pluginName: "DebugKit",
    socialLinks: [
      { icon: 'github', link: 'https://github.com/cakephp/debug_kit' },
    ],
    editLink: {
      pattern: 'https://github.com/cakephp/debug_kit/edit/5.x/docs/:path',
      text: 'Edit this page on GitHub',
    },
    sidebar: tocEn,
    nav: [
      { text: 'CakePHP', link: 'https://cakephp.org' },
      { text: 'API', link: 'https://api.cakephp.org/debugkit/' },
      { ...versions },
    ],
  },
  locales: {
    root: {
      label: 'English',
      lang: 'en',
      themeConfig: {
        sidebar: tocEn,
      },
    },
    fr: {
      label: 'Français',
      lang: 'fr',
      themeConfig: {
        sidebar: tocFr,
      },
    },
    ja: {
      label: '日本語',
      lang: 'ja',
      themeConfig: {
        sidebar: tocJa,
      },
    },
    pt: {
      label: 'Português',
      lang: 'pt',
      themeConfig: {
        sidebar: tocPt,
      },
    },
  },
}
