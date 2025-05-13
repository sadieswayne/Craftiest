<div class="modal modal-blur fade" id="updatePopup" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content overflow-hidden">
            <div class="modal-header ps-6 pe-14">
                <h5 class="modal-title">There is an update!</h5>
                <button type="button" class="btn-close !right-auto end-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>New Update is Available.</h4>
                    Version <span id="update_version"></span>
                <p>
                    Update Notes;
                    <br>
                    <span id="update_description"></span>
                </p>
                <p id="update_status"></p>
            </div>
			<div class="modal-footer grid grid-cols-2 p-0 gap-0">
				<button id="update_btn" class="btn btn-success py-[0.75rem] m-0 roundehidden w-full shadow-2xl shadow-green-400" type="button" onclick="update();">
					{{__('Update')}}
				</button>
				<button type="button" class="btn btn-danger py-[0.75rem] m-0 rounded-none w-full" onclick="hide_update_alert()" data-bs-dismiss="modal">
                    {{__('Hide Alert')}}
                </button>
			</div>
        </div>
    </div>
</div>
