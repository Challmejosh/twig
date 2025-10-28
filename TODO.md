# TODO: Convert React Project to PHP/Twig

## Information Gathered
- Current project has React frontend with components, hooks, context, routing.
- PHP backend with Auth.php, TicketManager.php, Router.php.
- Twig templates for rendering.
- Need to convert React components to PHP logic and render via Twig.
- Match React UI exactly: animations, forms, validations, modals, etc.

## Plan
1. **Remove React Dependencies and Files**
   - Update package.json to remove React deps.
   - Delete React-specific files (src/*.tsx, src/components/, src/features/, etc.).
   - Keep PHP files and Twig templates.

2. **Enhance PHP Backend**
   - Update Auth.php with advanced validation matching React.
   - Update Router.php to handle all routes with proper logic.
   - Update TicketManager.php if needed.

3. **Update Twig Templates to Match React UI**
   - Update base.twig with animations and notifications.
   - Update landing.twig with exact content and animations.
   - Update auth templates (signin.twig, signup.twig) with forms matching React.
   - Update dashboard.twig with stats and animations.
   - Update ticket-related templates with modals, edit modes.
   - Add mobile navigation.

4. **Add Missing Features in PHP/Twig**
   - Implement confirm modals for delete.
   - Add edit mode toggle.
   - Match form validations and error messages.
   - Add logout in navigation.

5. **Update Entry Point**
   - Ensure public/index.php is the main entry, remove React build.

## Dependent Files to Edit
- package.json
- src/Auth.php
- src/Router.php
- src/TicketManager.php
- templates/*.twig
- public/index.php
- Delete React files after conversion.

## Followup Steps
- Test all routes.
- Verify responsive design.
- Check animations.
- Run PHP server.
- Ensure no React dependencies remain.
