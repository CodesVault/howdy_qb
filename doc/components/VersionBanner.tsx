import useVersion from '../hooks/useVersion'

export default function VersionBanner() {
	const version = useVersion();

	if (!version) return null;

	return <>WP QueryBuilder v{version} is released 🎉</>
}
