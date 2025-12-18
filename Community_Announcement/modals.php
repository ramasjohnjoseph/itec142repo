<div id="eventModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4">
    <div class="modal-dialog bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl transform transition-all duration-300 opacity-0 scale-95">
        <h3 class="text-xl font-bold mb-4 text-green-800">Event Details</h3>
        <form action="process_manage.php" method="POST" class="space-y-4">
            <input type="hidden" name="type" value="event">
            <input type="hidden" name="action" id="eventAction">
            <input type="hidden" name="id" id="eventId">

            <input type="text" name="event_title" id="mEventTitle" placeholder="Event Title" required class="w-full border p-3 rounded-xl outline-none focus:ring-2 focus:ring-green-500">
            <input type="date" name="event_date" id="mEventDate" required class="w-full border p-3 rounded-xl outline-none focus:ring-2 focus:ring-green-500">
            <input type="text" name="event_location" id="mEventLoc" placeholder="Location" required class="w-full border p-3 rounded-xl outline-none focus:ring-2 focus:ring-green-500">
            <textarea name="event_description" id="mEventDesc" placeholder="Description" rows="3" class="w-full border p-3 rounded-xl outline-none focus:ring-2 focus:ring-green-500"></textarea>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('eventModal')" class="px-5 py-2 text-gray-500 font-semibold">Cancel</button>
                <button type="submit" class="bg-green-700 text-white px-8 py-2 rounded-full font-bold hover:bg-green-800">Save Event</button>
            </div>
        </form>
    </div>
</div>

<div id="annModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4">
    <div class="modal-dialog bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl transform transition-all duration-300 opacity-0 scale-95">
        <h3 class="text-xl font-bold mb-4 text-green-800">Announcement</h3>
        <form action="process_manage.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="type" value="announcement">
            <input type="hidden" name="action" id="annAction">
            <input type="hidden" name="id" id="annId">

            <input type="text" name="announcement_title" id="mAnnTitle" placeholder="Title" required class="w-full border p-3 rounded-xl outline-none focus:ring-2 focus:ring-green-500">
            <textarea name="announcement_content" id="mAnnContent" placeholder="What's on your mind?" rows="4" class="w-full border p-3 rounded-xl outline-none focus:ring-2 focus:ring-green-500"></textarea>
            
            <div class="p-3 bg-gray-50 rounded-xl border border-dashed">
                <p class="text-[10px] uppercase font-bold text-gray-400 mb-2">Attached Photo</p>
                <img id="annImagePreview" src="" class="hidden w-full h-32 object-cover rounded shadow-sm mb-2">
                <input type="file" name="announcement_image" accept="image/*" class="text-xs">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('annModal')" class="px-5 py-2 text-gray-500 font-semibold">Cancel</button>
                <button type="submit" class="bg-green-700 text-white px-8 py-2 rounded-full font-bold hover:bg-green-800">Post Now</button>
            </div>
        </form>
    </div>
</div>