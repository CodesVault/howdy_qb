import { useEffect, useState } from 'react'

export default function VersionBanner() {
	const [version, setVersion] = useState('');

	useEffect(() => {
		fetch('https://api.github.com/repos/CodesVault/howdy_qb/releases/latest')
			.then(res => res.ok ? res.json() : null)
			.then(data => {
				if (data?.tag_name) {
					setVersion(data.tag_name);
				}
			})
			.catch(() => {});
	}, []);

	if (!version) return null;

	return <>WP QueryBuilder v{version} is released 🎉</>
}
