import styles from '../styles.css'
import { ThemeProvider } from 'next-themes'

export default function MyApp({ Component, pageProps }) {
  return (
    <>
      <ThemeProvider attribute="class">
        <Component {...pageProps} />
      </ThemeProvider>
    </>
  )
}
