# Markdown page

휴고로 마크다운 파일을 html 파일로 빌드한 후 드루팔 페이지로 사용합니다.

## 동작 방식

/hugo/content 디렉토리 경로를 상태로 저장한 후 디렉토리 내파일 변경 상태와 비교하여
URL 별칭을 동기화하여 서비스합니다.
frontmatter에 기록한 임의의 데이터는 휴고가 index.json 형태로 빌드하고
이 모듈이 읽어들여서 마크다운 페이지에 넣어줍니다.

## 동기화

```bash
# 현재 파일 상태를 저장하고 path_alias를 동기화합니다.
$ drush hugo_page:build (hpb)
# 현재 파일 상태, path_alias를 삭제합니다.
$ drush hugo_page:purge (hpp)
```

- $active: /hugo/content 디렉토리 파일 경로
- $staged: state (table:key_value/collection:state/name:hugo_page.paths)
