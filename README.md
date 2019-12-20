# Features

- Clients: Lastfm & Discogs
- Job processes for:
  - Lastfm: artists, albums, tracks
  - Discogs: artists, albums, labels
- Lastfm guards against empty artist, album & tracks
- Repository pattern
- Service provicers
- Featured models:
  - Company
  - Alias
  - NameVariation
  - Member
  - Barcode
  - AlbumFormat
  - Url
- No API or web routes

- Strong points:
  - Client, console and job separation
  - Client divided in different classes according to model type
- Caveats:
  - All models in one place
  - Specific-provider features live in main application folder